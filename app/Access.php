<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Access extends Model
{
    use HasApiTokens, Notifiable;

    protected $table = 'access_id';

    /*protected $fillable = [
        'id', 'user_id', 'rfid_id',
    ];*/

}
