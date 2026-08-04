<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailHistory extends Model
{
    use HasFactory;
    protected $fillable = ['tag','template','mail_to', 'subject', 'mail_body','status'];

    public function mail_template()
    {
        return $this->belongsTo(MailTemplate::class, 'template', 'id');
    }
}
