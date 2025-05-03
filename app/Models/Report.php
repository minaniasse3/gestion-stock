<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'format',
        'period_start',
        'period_end',
        'created_by',
        'file_path',
        'status',
        'error',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'period_start' => 'datetime',
        'period_end' => 'datetime',
    ];

    /**
     * Relation avec l'utilisateur qui a créé le rapport
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
} 