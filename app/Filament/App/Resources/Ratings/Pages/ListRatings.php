<?php

namespace App\Filament\App\Resources\Ratings\Pages;

use App\Filament\App\Resources\Ratings\RatingResource;
use Filament\Resources\Pages\ListRecords;

class ListRatings extends ListRecords
{
    protected static string $resource = RatingResource::class;
}
