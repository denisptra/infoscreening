<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('type')
                    ->required()
                    ->default('Pemutaran'),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('venue'),
                TextInput::make('city'),
                DateTimePicker::make('starts_at')
                    ->required(),
                DateTimePicker::make('ends_at'),
                FileUpload::make('image')
                    ->image(),
                TextInput::make('url')
                    ->url(),
                Toggle::make('is_published')
                    ->required(),
            ]);
    }
}
