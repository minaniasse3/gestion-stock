<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'contact_person',
    ];

    /**
     * Relation avec les produits de ce fournisseur
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Relation avec les activités concernant ce fournisseur
     */
    public function activities()
    {
        return $this->morphMany(ActivityLog::class, 'related');
    }
}
