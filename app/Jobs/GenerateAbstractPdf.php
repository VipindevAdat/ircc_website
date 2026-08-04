<?php

namespace App\Jobs;

use App\Models\AbstractSubmission;
use App\Models\Category;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class GenerateAbstractPdf
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $abstract;

    /**
     * Create a new job instance.
     */
    public function __construct(AbstractSubmission $abstract)
    {
        $this->abstract = $abstract;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('abstract_mail: GenerateAbstractPdf started', [
            'abstract_id' => $this->abstract->id,
            'registration_id' => $this->abstract->registration_id,
            'pid' => $this->abstract->pid,
        ]);

        try {
            $this->generateAndStorePdf();
        } catch (Throwable $e) {
            Log::error('abstract_mail: GenerateAbstractPdf failed (confirmation email is sent separately)', [
                'abstract_id' => $this->abstract->id,
                'message' => $e->getMessage(),
                'exception' => $e::class,
            ]);
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }

    private function generateAndStorePdf(): void
    {
        $category = Category::where('id', $this->abstract->category_id)->first();
        $categoryPrefix = $category ? $category->prefix : substr($this->abstract->category_id, 0, 1);

        $lastAbstract = AbstractSubmission::latest()->first();
        $lastIndex = $lastAbstract ? $lastAbstract->abstract_index : null;

        if (!empty($lastIndex)) {
            $index = intval(substr($lastIndex, strpos($lastIndex, '-') + 1)) + 1;
        } else {
            $index = $category ? $category->last_number : 1;
        }
        $abstract_index = $categoryPrefix . '-' . sprintf('%03d', $index);
        Log::info('abstract_mail: GenerateAbstractPdf rendering DomPDF', [
            'abstract_id' => $this->abstract->id,
            'abstract_index' => $abstract_index,
        ]);
        $pdf = Pdf::loadView('mail.abstract-pdf', ['result' => $this->abstract, 'id' => $abstract_index]);
        $pdf->setPaper('A4', 'landscape');
        $output = $pdf->output();
        Log::info('abstract_mail: GenerateAbstractPdf DomPDF output generated', [
            'abstract_id' => $this->abstract->id,
            'bytes' => strlen($output),
        ]);

        $abstractId = $this->abstract->id;
        $previousFile = $this->abstract->abstract_file;
        if (! empty($previousFile) && Storage::disk('uploads')->exists('abstract/' . $previousFile)) {
            Storage::disk('uploads')->delete('abstract/' . $previousFile);
        }

        $fileName = $abstractId . $abstract_index . '_proposal.pdf';
        Storage::disk('uploads')->put('abstract/' . $fileName, $output);

        AbstractSubmission::withoutEvents(function () use ($fileName): void {
            $this->abstract->update(['abstract_file' => $fileName]);
        });

        if ($category) {
            $category->update(['last_number' => $index + 1]);
        }

        Log::info('abstract_mail: GenerateAbstractPdf finished', [
            'abstract_id' => $this->abstract->id,
            'abstract_file' => $fileName,
        ]);
    }
}
