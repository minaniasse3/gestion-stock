<?php

namespace App\Exports;

use App\Models\StockMovement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StockMovementsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $movements;

    public function __construct($movements = null)
    {
        $this->movements = $movements;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        if ($this->movements) {
            return $this->movements;
        }
        
        return StockMovement::with(['product', 'user'])->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Produit',
            'Quantité',
            'Type',
            'Raison',
            'Utilisateur',
            'Référence',
            'Date'
        ];
    }

    /**
     * @param mixed $movement
     * @return array
     */
    public function map($movement): array
    {
        return [
            $movement->id,
            $movement->product ? $movement->product->name : 'Produit inconnu',
            $movement->quantity,
            $movement->type,
            $movement->reason,
            $movement->user ? $movement->user->name : 'Utilisateur inconnu',
            $movement->reference,
            $movement->created_at->format('d/m/Y H:i')
        ];
    }
} 