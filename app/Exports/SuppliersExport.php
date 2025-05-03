<?php

namespace App\Exports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SuppliersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $suppliers;

    public function __construct($suppliers = null)
    {
        $this->suppliers = $suppliers;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        if ($this->suppliers) {
            return $this->suppliers;
        }
        
        return Supplier::withCount('products')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nom',
            'Personne de contact',
            'Email',
            'Téléphone',
            'Adresse',
            'Nombre de produits',
            'Date de création',
            'Dernière mise à jour'
        ];
    }

    /**
     * @param mixed $supplier
     * @return array
     */
    public function map($supplier): array
    {
        return [
            $supplier->id,
            $supplier->name,
            $supplier->contact_person,
            $supplier->email,
            $supplier->phone,
            $supplier->address,
            $supplier->products_count ?? count($supplier->products),
            $supplier->created_at->format('d/m/Y'),
            $supplier->updated_at->format('d/m/Y')
        ];
    }
} 