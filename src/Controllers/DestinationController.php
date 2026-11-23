<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Destinations;
use App\HttpException;

final class DestinationController
{
    public static function index(): void
    {
        $q = mb_substr(trim((string) ($_GET['q'] ?? '')), 0, 100);
        $region = (string) ($_GET['region'] ?? '');
        $sort = (string) ($_GET['sort'] ?? 'featured');
        $maxRaw = trim((string) ($_GET['max_price'] ?? ''));

        $regions = Destinations::regions();
        if (!in_array($region, $regions, true)) {
            $region = '';
        }
        if (!array_key_exists($sort, Destinations::SORTS)) {
            $sort = 'featured';
        }
        $maxPrice = is_numeric($maxRaw) && (float) $maxRaw > 0 ? (float) $maxRaw : null;

        view('destinations/index', [
            'title' => 'Destinations',
            'destinations' => Destinations::search($q, $region, $maxPrice, $sort),
            'regions' => $regions,
            'filters' => ['q' => $q, 'region' => $region, 'max_price' => $maxPrice, 'sort' => $sort],
        ]);
    }

    public static function show(string $slug): void
    {
        $destination = Destinations::findBySlug($slug);
        if (!$destination) {
            throw new HttpException(404, 'We could not find that destination.');
        }
        view('destinations/show', [
            'title' => $destination['name'] . ', ' . $destination['country'],
            'destination' => $destination,
            'usesMap' => true,
        ]);
    }
}
