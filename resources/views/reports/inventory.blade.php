<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport d'inventaire</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #333;
            margin: 0;
        }
        .header p {
            color: #666;
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
        .summary {
            margin: 20px 0;
        }
        .summary div {
            margin-bottom: 5px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rapport d'inventaire</h1>
        <p>Généré le: {{ $date->format('d/m/Y H:i') }}</p>
        <p>{{ $report->name }}</p>
    </div>

    <div class="summary">
        <div><strong>Nombre total de produits:</strong> {{ count($products) }}</div>
        <div><strong>Valeur totale de l'inventaire:</strong> {{ number_format($products->sum(function($product) { return $product->price * $product->quantity; }), 2) }} €</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Catégorie</th>
                <th>Fournisseur</th>
                <th>Prix (€)</th>
                <th>Quantité</th>
                <th>Valeur (€)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{ $product->id }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category }}</td>
                <td>{{ $product->supplier ? $product->supplier->name : 'Non défini' }}</td>
                <td>{{ number_format($product->price, 2) }}</td>
                <td>{{ $product->quantity }}</td>
                <td>{{ number_format($product->price * $product->quantity, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Système de gestion de stock - Rapport généré automatiquement</p>
    </div>
</body>
</html> 