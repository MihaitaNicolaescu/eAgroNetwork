<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostVotes extends Model
{
    use HasFactory;
    public $timestamps = false;// disable created_at and edited_at in Post Votes table in DB
}
