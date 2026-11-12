<?php

namespace App\Filament\Resources\Articles\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')
                    ->default(fn () => auth()->id() ?? 1),

                TextInput::make('title')
                    ->label('Judul Artikel')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('slug', Str::slug($state));
                    }),

                TextInput::make('slug')
                    ->label('Slug / URL')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                Select::make('status')
                    ->label('Status Publikasi')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ])
                    ->default('draft')
                    ->required(),

                Toggle::make('is_featured')
                    ->label('Artikel Utama / Featured')
                    ->default(false),

                Select::make('categories')
                    ->label('Rubrik Kategori')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->preload(),

                Select::make('tags')
                    ->label('Label / Tags')
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->preload(),

                DateTimePicker::make('published_at')
                    ->label('Waktu Publikasi')
                    ->default(now()),

                FileUpload::make('featured_image')
                    ->label('Foto Sampul / Banner')
                    ->image()
                    ->disk('public')
                    ->directory('articles')
                    ->columnSpanFull(),

                Textarea::make('excerpt')
                    ->label('Ringkasan / Excerpt')
                    ->rows(3)
                    ->columnSpanFull(),

                RichEditor::make('content')
                    ->label('Isi Konten Artikel')
                    ->required()
                    ->columnSpanFull(),

                TextInput::make('reading_time')
                    ->label('Estimasi Baca (Menit)')
                    ->numeric()
                    ->default(4),

                TextInput::make('views')
                    ->label('Total Views')
                    ->numeric()
                    ->default(0)
                    ->disabled(),
            ]);
    }
}
