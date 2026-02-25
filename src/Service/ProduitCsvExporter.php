<?php

namespace App\Service;

use App\Entity\Produit;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProduitCsvExporter
{
    /**
     * @param Produit[] $produits
     */
    public function export(array $produits): StreamedResponse
    {
        $response = new StreamedResponse(function () use ($produits) {

            $handle = fopen('php://output', 'w+');

            // En-têtes CSV
            fputcsv($handle, ['Name', 'Description', 'Price'], ';');

            foreach ($produits as $produit) {
                fputcsv($handle, [
                    $produit->getName(),
                    $produit->getDescription(),
                    $produit->getPrice(),
                ], ';');
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set(
            'Content-Disposition',
            'attachment; filename="produits.csv"'
        );

        return $response;
    }
}