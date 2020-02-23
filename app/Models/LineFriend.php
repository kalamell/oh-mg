<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class LineFriend
 * @package App\Models
 *
 * @property int $id
 * @property string $line_id
 * @property string $display_name
 * @property string $reply_token
 */
class LineFriend extends Model
{
    protected $fillable = ['line_id', 'display_name', 'reply_token'];
}
