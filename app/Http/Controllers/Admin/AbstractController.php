<?php
namespace App\Http\Controllers\Admin;
use App\Models\AbstractDomain;
use App\Models\AbstractSubmission;
use App\Models\AbstractReviewer;
use App\Models\Registration;
use App\Models\Reviewer;
use App\Models\ReviewStatus;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Models\AccompanyPerson;
use App\Models\Category;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccompanyingVerify;
use App\Models\AccompanyPaymentDetail;
use App\Mail\Register;
use App\Mail\AdminRegister;
use App\Mail\UserAbstract;
use App\Mail\AdminAbstract;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\PhpWord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class AbstractController extends Controller
{
    public function index(Request $request, Datatables $datatables)
    {
    if ($request->ajax()) {

        $abstract_details = AbstractSubmission::whereHas('registration')->with('registration','category')->where('status',1)->orderBy('id','DESC')->select('abstracts.*');

        $enabledReviewers = Reviewer::where('status', 1)->get();


        return $datatables->eloquent($abstract_details)
            ->editColumn('additional_authors', function (AbstractSubmission $abstract) {
                $authors = explode(',', $abstract->additional_authors);
                return implode('<br>', $authors);
            })
            ->editColumn('abstract_title', function (AbstractSubmission $abstract) {
                return mb_substr(wordwrap($abstract->abstract_title ?? 'No Title Found',15,'<br>'),0,50,'UTF-8').'...';
            })
            ->editColumn('student_authors', function (AbstractSubmission $abstract) {
                $authors = $abstract->student_authors;

                if (is_array($authors)) {
                    return implode('<br>', $authors);
                } elseif (is_string($authors)) {
                    return implode('<br>', explode(',', $authors));
                }

                return 'No student authors';
            })
            ->addColumn('category', function (AbstractSubmission $abstract) {
                $category =  $abstract->category?$abstract->category->name:'-';
                return wordwrap($category,15,'<br>') ;
            })
            ->addColumn('abstract_domain', function ($row) {
                return $row->abstractDomain->domain ?? 'Unknown';
            })
            ->addColumn('review_status', function ($row) {
                $reviewer = $row->abstractReviewer()->first();
            
                if ($reviewer && $reviewer->review_status) {
                    $status = ReviewStatus::find($reviewer->review_status);
                    
                    return $status ? ($status->status=='Approved'?'<span class="badge bg-success">'.$status->status.'</span>':($status->status=='Rejected'?'<span class="badge bg-danger">'.$status->status.'</span>':'<span class="badge bg-warning">'.$status->status.'</span>' )): 'Reviewer Not Assigned';
                }
            
                return 'Unknown';
            })
            ->addColumn('reviewer', function ($data) use ($enabledReviewers) {
                $assignedReviewerId = $data->reviewers->first()?->id;
                $reviewStatus = $data->abstractReviewer()->first()?->review_status;
            
                $isDisabled = false;
                if ($reviewStatus) {
                    $status = \App\Models\ReviewStatus::find($reviewStatus);
                    $isDisabled = $status && in_array($status->status, ['Approved', 'Rejected']);
                }
            
                $options = '<select data-id="' . $data->id . '" class="reviewer-select" ' . ($isDisabled ? 'disabled' : '') . '>';
                $options .= '<option value="" disabled ' . (!$assignedReviewerId ? 'selected' : '') . '>Select a reviewer</option>';
            
                foreach ($enabledReviewers as $reviewer) {
                    $selected = ($assignedReviewerId == $reviewer->id) ? 'selected' : '';
                    $options .= '<option value="' . $reviewer->id . '" ' . $selected . '>' . $reviewer->first_name . ' ' . $reviewer->last_name . '</option>';
                }
            
                $options .= '</select>';
                return $options;
            })
            
            ->addColumn('registration_number', function (AbstractSubmission $abstract) {
                return $abstract->registration->registration_number;
            })


            ->addColumn('institute_name', function (AbstractSubmission $abstract) {
                return wordwrap($abstract->registration->institution_name,15,'<br>');
            })
            ->addColumn('action', function (AbstractSubmission $abstract) {
                $abstractExist=0;
                if(!empty($abstract->abstract_file)){
                    if (Storage::disk('uploads')->exists('abstract/' . $abstract->abstract_file)) {
                        $abstractExist=1;
                    }
                }
           
                if($abstract->abstract_content && $abstractExist )
                return '<a style="cursor: pointer;display:none" href="' . route('admin.abstract.edit', $abstract->id) . '" class="btn btn-sm btn-clean btn-icon text-end" title="Edit" data-toggle="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                    <a style="cursor: pointer;display:none" data-id="'.$abstract->id.'"  data-href="' . route("admin.abstract.destroy", $abstract->id) . '" class="delete-abstract"  data-bs-toggle="modal" data-bs-target="#delete-modal" id="delete-abstract" ><i class="bi bi-trash"></i></a>
                        <a style="font-size: 20px ;margin-rightfa-file-pdf-o: 10px"href="'.route('admin.wordDownload', ['id' => $abstract->id]).'""><i class="bi bi-file-earmark-word-fill"></i></a>
                        <a style="color: red; font-size: 20px"href="'.route('admin.download.pdf', ['abstractId' => $abstract->id]).'""><i class="fa fa-file-pdf-o"></i></a>';
                elseif($abstract->abstract_content)
                    return '<a style="cursor: pointer;display:none" href="' . route('admin.abstract.edit', $abstract->id) . '" class="btn btn-sm btn-clean btn-icon text-end" title="Edit" data-toggle="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                        <a style="cursor: pointer;display:none" data-id="'.$abstract->id.'"  data-href="' . route("admin.abstract.destroy", $abstract->id) . '" class="delete-abstract"  data-bs-toggle="modal" data-bs-target="#delete-modal" id="delete-abstract" ><i class="bi bi-trash"></i></a>
                        <a style="font-size: 20px ;margin-rightfa-file-pdf-o: 10px"href="'.route('admin.wordDownload', ['id' => $abstract->id]).'""><i class="bi bi-file-earmark-word-fill"></i></a>
                            <a style="color: red; font-size: 20px" href="'.route('admin.irccPdf', ['id' => $abstract->id]).'" title="Download PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>';
                else
                return '<a style="cursor: pointer;display:none" href="' . route('admin.abstract.edit', $abstract->id) . '" class="btn btn-sm btn-clean btn-icon text-end" title="Edit" data-toggle="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                <a style="cursor: pointer;display:none" data-id="'.$abstract->id.'" data-href="' . route("admin.abstract.destroy", $abstract->id) . '" class="delete-abstract"   data-bs-toggle="modal" data-bs-target="#delete-modal" id="delete-abstract" ><i class="bi bi-trash"></i>
            </a><a style="cursor: pointer;display:none" href="'.route('admin.irccPdf', ['id' => $abstract->id]).'" title="Download PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>';


            })
            ->addColumn('row_style', function (AbstractSubmission $abstract) {
                $abstractExist=0;
                if(!empty($abstract->abstract_file)){
                    if (Storage::disk('uploads')->exists('abstract/' . $abstract->abstract_file)) {
                        $abstractExist=1;
                    }
                }
                return $abstractExist?'':'background-color: #f8d7da;' ;
            })
            ->rawColumns(['action','abstract_title','category','registration_number','institute_name','reviewer', 'review_status', 'student_authors','row_style'])
            ->make(true);
        }

            return view('admin.abstract.list');


    }
    public function edit(string $id)
    {

        return view('admin.abstract.form')->with([
            'abstract' => AbstractSubmission::whereId($id)->first(),

        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $abstract=AbstractSubmission::whereId($id)->first();
        $abstract->update($request->all());
        return redirect()->route('admin.abstract.abstractdetails')->with(['success_message'=>'Abstract updated succesfully']);;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $abstract=AbstractSubmission::where('id',$id)->delete();
            return response()
        ->json(["success" => "Abstract deleteted successfully"], 200);
    }

    /**
     * Revert a submitted abstract to draft so the student can edit and resubmit.
     */
    public function revertToDraft(AbstractSubmission $abstract)
    {
        if ((int) $abstract->status !== 1) {
            return redirect()
                ->back()
                ->with('error', 'Only submitted abstracts can be reverted to draft.');
        }

        DB::transaction(function () use ($abstract) {
            $abstract->update(['status' => 0]);

            $pivot = AbstractReviewer::where('abstract_id', $abstract->id)->first();
            if ($pivot) {
                $pivot->update([
                    'review_status' => null,
                    'score' => null,
                    'comments' => null,
                    'clarity' => null,
                    'relevance' => null,
                    'originality' => null,
                ]);
            }
        });

        return redirect()
            ->back()
            ->with(
                'success_message',
                'Abstract reverted to draft. The student can edit and resubmit. It will not appear in the submitted abstracts list until submitted again.'
            );
    }

    public function abstractView(Request $request) {
        $abstract = AbstractSubmission::find($request->id);

        if (!$abstract) {
            return response()->json(['error' => 'Abstract not found'], 404);
        }

        $student_authors = $this->decodeJsonField($abstract->student_authors);
        $additional_authors = $this->splitCommaSeparatedValues($abstract->additional_authors);
        $registration = Registration::find($abstract->registration_id);
        $category = $abstract->category->name;
        $abstractDomain = AbstractDomain::find($abstract->abstract_domain);

        $reviews = $abstract->abstractReviewer()->with('reviewer')->first();   

        $data = [
            'title' => $abstract->abstract_title ?? 'Untitled',
            'content' => $abstract->abstract_content ?? 'No content available.',
            'student_authors' => $student_authors,
            'additional_authors' => $additional_authors,
            'category' => $category ?? 'NA',
            'abstractDomain' => $abstractDomain,
            'institute_name' => $registration->institution_name ?? 'Unknown Institution',
            'correspondent_author' => $abstract->correspondent_author ?? 'No corresponding author specified.',
            'reviews' => $reviews,
        ];

        return view('admin.components.viewAbstract', $data);
    }

    /**
     * Decode a JSON field safely and return an array.
     *
     * @param  mixed  $field
     * @return array
     */
    private function decodeJsonField($field) {
        if (is_string($field)) {
            return json_decode($field, true) ?? [];
        }

        return is_array($field) ? $field : [];
    }

    private function splitCommaSeparatedValues($field) {
        if (is_string($field) && !empty($field)) {
            return array_map('trim', explode(',', $field));
        }

        return [];
    }

    /**
     * Embed an image in a PhpWord container; skip on failure so exports do not 500
     * (remote URLs often fail validation when unreachable or returning HTML).
     *
     * @param  \PhpOffice\PhpWord\Element\AbstractContainer  $container
     */
    private function addImageToContainer($container, string $source, array $style): void
    {
        if ($source === '') {
            Log::info('wordDownload: logo skipped because source is empty');
            return;
        }
        if (!is_file($source)) {
            Log::warning('wordDownload: logo file not found', ['source' => $source]);
            return;
        }
        $imageInfo = @getimagesize($source);
        if ($imageInfo === false) {
            Log::warning('wordDownload: logo file is not a readable image', ['source' => $source]);
            return;
        }
        try {
            $container->addImage($source, $style);
            Log::info('wordDownload: logo embedded', [
                'source' => $source,
                'mime' => $imageInfo['mime'] ?? null,
                'width' => $imageInfo[0] ?? null,
                'height' => $imageInfo[1] ?? null,
            ]);
        } catch (\Throwable $e) {
            Log::warning('wordDownload: could not embed image in Word export', [
                'source' => $source,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Return the first existing image path from candidate relative paths.
     */
    private function firstExistingImagePath(array $candidates): string
    {
        foreach ($candidates as $relativePath) {
            $absolutePath = $this->publicWebPath($relativePath);
            if (is_file($absolutePath)) {
                return $absolutePath;
            }
        }

        return '';
    }

    private function addWatermarkToSection(Section $section, string $source): void
    {
        if ($source === '' || !is_file($source) || @getimagesize($source) === false) {
            Log::warning('wordDownload: watermark skipped (invalid image source)', ['source' => $source]);
            return;
        }

        try {
            $section->addWatermark($source, [
                'width' => 380,
                'height' => 260,
                'marginTop' => 180,
                'marginLeft' => 120,
            ]);
            Log::info('wordDownload: watermark embedded', ['source' => $source]);
        } catch (\Throwable $e) {
            Log::warning('wordDownload: failed to add watermark', [
                'source' => $source,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Absolute filesystem path for a URL path like front/images/logo.png.
     * Production uses public_html/ as the web root; an empty public/ would break public_path().
     */
    private function publicWebPath(string $relative): string
    {
        $relative = ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative), DIRECTORY_SEPARATOR);
        if (is_dir(base_path('public_html'))) {
            return base_path('public_html'.DIRECTORY_SEPARATOR.$relative);
        }

        return public_path($relative);
    }

    public function wordDownload(Request $request)
    {
        $abstract = AbstractSubmission::where('id', $request->id)->firstOrFail();

        // Match irccPdf: category is linked by category_id (not legacy `category` name / JSON).
        $category = Category::where('id', $abstract->category_id)->first();
        $rawPrefix = ($category && $category->prefix !== null && $category->prefix !== '')
            ? (string) $category->prefix
            : 'abs';
        $categoryPrefix = preg_replace('/[^A-Za-z0-9_-]+/', '', $rawPrefix) ?: 'abs';
        $index = (int) ($category?->last_number ?? 1);
        $abstract_index = $categoryPrefix.'-'.sprintf('%03d', $index);

        $registration = Registration::where('id', $abstract->registration_id)->first();
        if (! $registration) {
            abort(404, 'Registration not found for this abstract.');
        }
        // Use only local files for Word export to avoid remote/SSL instability.
        $stemLogoSource = $this->firstExistingImagePath([
            'front/images/logo-stem4girls.png',
            'front/images/stem4girls-logo.png',
        ]);
        $gyraLogoSource = $this->firstExistingImagePath([
            'front/images/logo-gyra.png',
            'front/images/logo-gyra-inverse.png',
        ]);
        $irccLogoSource = $this->firstExistingImagePath([
            'front/images/unbound-logo.png',
            'front/images/unbound-logo1.png',
        ]);
        Log::info('wordDownload: resolved logo paths', [
            'stem' => $stemLogoSource,
            'gyra' => $gyraLogoSource,
            'ircc' => $irccLogoSource,
        ]);
        $phpWord = new PhpWord();

        $studentAuthors = is_array($abstract->student_authors)
            ? implode(', ', $abstract->student_authors)
            : $abstract->student_authors;

        $additionalAuthors = $request->has('additional_authors')
            ? (is_array($request->additional_authors)
                ? implode(',', $request->additional_authors)
                : (string) $request->additional_authors)
            : (string) ($abstract->additional_authors ?? '');

        $section = $phpWord->addSection();
        $this->addWatermarkToSection($section, $irccLogoSource);

        $section->addText('International Research Conference for Children 2026 (IRCC\'25)',array('name'=>'Arial','size' => 15,'bold' => true),array('align'=>'center'));
        $section->addTextBreak(1);
        $section->addText('Title: '.$abstract->abstract_title,array('name'=>'Arial','size' => 13,'bold' => true),array('align'=>'center'));
        $section->addText($studentAuthors, array('name' => 'Arial', 'size' => 13, 'bold' => true), array('align' => 'center'));
        $section->addText('(Additional Authors)'.$additionalAuthors, array('name' => 'Arial', 'size' => 13, 'bold' => true), array('align' => 'center'));
        $section->addText((string) ($abstract->affiliation ?? ''), array('name'=>'Arial','size' => 13,'bold' => true), array('align'=>'center'));
        $section->addText($registration->email,array('name'=>'Arial','size' => 13,'bold' => true),array('align'=>'center'));
        $section->addText('(Corresponding author),'.$registration->first_name.' '.$registration->last_name ,array('name'=>'Arial','size' => 13,'bold' => true),array('align'=>'center'));
        $section->addTextBreak(1);
        $phpWord->addFontStyle('r2Style', array('bold'=>false, 'italic'=>false, 'size'=>12));
        $phpWord->addParagraphStyle('p2Style', array('align'=>'left'));
        $section->addText('Abstract: '.$abstract->abstract_content,array('name'=>'Arial','size' => 12,'bold' => false),array('align'=>'left'));
        $section->addTextBreak(2);


        $section->addText('References',array('name'=>'Arial','size' => 12,'bold' => false),array('align'=>'left'));
        $section->addTextBreak(1);
        $references=json_decode($abstract->references);
        if(!empty($references)){
        foreach($references as $reference){
            $section->addText($reference,array('name'=>'Arial','size' => 12,'bold' => false),array('align'=>'left'));
        }
        }
       
        $section->addTextBreak(1);

        $downloadBaseName = sprintf('%d_%s_proposal.docx', $abstract->id, $abstract_index);
        $tempDir = storage_path('app/temp');
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        $fullPath = $tempDir.DIRECTORY_SEPARATOR.$downloadBaseName;

        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($fullPath);

        if (! is_file($fullPath)) {
            Log::error('wordDownload: docx was not written', ['path' => $fullPath]);

            abort(500, 'Could not generate Word document.');
        }

        return response()->download($fullPath, $downloadBaseName)->deleteFileAfterSend(true);

    }
    function printSeparator(Section $section): void
    {
        $section->addTextBreak();
        $lineStyle = ['weight' => 0.2, 'width' => 150, 'height' => 0, 'align' => 'center'];
        $section->addLine($lineStyle);
        $section->addTextBreak(2);
    }

    public function irccPdf($id)
    {
        try {
            Log::info('irccPdf: start', ['abstract_id' => $id]);
            // Dompdf can be memory-intensive for long abstracts / rich HTML.
            // Raise limit only for this request and tune renderer options.
            @ini_set('memory_limit', '512M');
            @set_time_limit(120);

            $abstract = AbstractSubmission::with('registration')->findOrFail($id);

            $category = Category::where('id', $abstract->category_id)->first();
            $rawPrefix = ($category && $category->prefix !== null && $category->prefix !== '')
                ? (string) $category->prefix
                : 'abs';
            $categoryPrefix = preg_replace('/[^A-Za-z0-9_-]+/', '', $rawPrefix) ?: 'abs';

            $lastAbstract = AbstractSubmission::latest()->first();
            $lastIndex = $lastAbstract ? $lastAbstract->abstract_index : null;

            if (!empty($lastIndex) && str_contains((string) $lastIndex, '-')) {
                $index = intval(substr((string) $lastIndex, strpos((string) $lastIndex, '-') + 1)) + 1;
            } else {
                $index = $category ? (int) $category->last_number : 1;
            }

            $abstract_index = $categoryPrefix . '-' . sprintf('%03d', $index);

            $pdf = Pdf::setOption([
                'dpi' => 72,
                'isFontSubsettingEnabled' => true,
                'isRemoteEnabled' => false,
                'isHtml5ParserEnabled' => true,
            ])->loadView('mail.abstract-pdf', ['result' => $abstract, 'id' => $abstract_index]);
            $pdf->setPaper('A4', 'landscape');
            $output = $pdf->output();

            $fileName = $id . '_' . $abstract_index . '_proposal.pdf';
            $filePath = 'abstract/' . $fileName;
            $writeOk = Storage::disk('uploads')->put($filePath, $output);
            if (!$writeOk) {
                Log::error('irccPdf: failed to write PDF to uploads disk', [
                    'abstract_id' => $abstract->id,
                    'relative_path' => $filePath,
                ]);
                abort(500, 'Could not write PDF file.');
            }

            $abstract->update(['abstract_file' => $fileName]);

            if ($category) {
                $category->update(['last_number' => $index + 1]);
            }

            $fileDownload = Storage::disk('uploads')->path($filePath);
            if (!is_file($fileDownload)) {
                Log::error('irccPdf: generated PDF not found on uploads disk', [
                    'abstract_id' => $abstract->id,
                    'disk_path' => $fileDownload,
                    'relative_path' => $filePath,
                ]);
                abort(500, 'Could not generate PDF file.');
            }

            try {
                Mail::to($abstract->registration->email)->send(new UserAbstract($abstract, $abstract->registration));
            } catch (\Throwable $mailError) {
                Log::warning('irccPdf: generated PDF but failed to send email', [
                    'abstract_id' => $abstract->id,
                    'error' => $mailError->getMessage(),
                ]);
            }

            Log::info('irccPdf: success', [
                'abstract_id' => $abstract->id,
                'relative_path' => $filePath,
            ]);

            return response()->download($fileDownload, $fileName);
        } catch (\Throwable $e) {
            Log::error('irccPdf: failed', [
                'abstract_id' => $id,
                'message' => $e->getMessage(),
                'exception' => $e::class,
            ]);
            throw $e;
        }
    }

    public function assignReviewer(Request $request)
        {
            $request->validate([
                'abstract_id' => 'required|exists:abstracts,id',
                'reviewer_id' => 'required|exists:reviewers,id',
            ]);

            $abstract = AbstractSubmission::find($request->abstract_id);
            $reviewer = Reviewer::find($request->reviewer_id);

            $existingReviewer = AbstractReviewer::where('abstract_id', $abstract->id)
                                                ->where('reviewer_id', $reviewer->id)
                                                ->first();
            if ($existingReviewer) {
                return response()->json(['error' => 'This reviewer is already assigned to this abstract.'], 400);
            }

            AbstractReviewer::create([
                'abstract_id' => $abstract->id,
                'reviewer_id' => $reviewer->id,
                'score' => null,
                'clarity' => null,
                'relevance' => null,
                'originality' => null,
                'comments' => null,
            ]);

            return response()->json(['success' => 'Reviewer assigned successfully.']);
        }



    }
