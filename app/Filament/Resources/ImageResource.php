<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Image;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ImageResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ImageResource\RelationManagers;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class ImageResource extends Resource
{
    protected static ?string $model = Image::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('filename')
                    ->label('Filename')
                    ->required(),
                FileUpload::make('url')
                    ->disk('s3')
                    ->visibility('public')
                    ->preserveFilenames()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('filename')
                    ->label('Filename'),
                ImageColumn::make('url')->label('Image')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    protected function handleFileDelete($record)
    {
        try {
            // Ambil path file dari database
            $filePath = $record->url; // Sesuaikan dengan nama kolom Anda
            
            // Pastikan path file ada
            if (!empty($filePath)) {
                // Hapus file dari S3
                if (Storage::disk('s3')->exists($filePath)) {
                    Storage::disk('s3')->delete($filePath);
                }
                
                // Log untuk debugging
                \Log::info('File deleted from S3: ' . $filePath);
            }
        } catch (\Exception $e) {
            \Log::error('Error deleting file from S3: ' . $e->getMessage());
            throw $e;
        }3
    }

    // Override method deleteAction di Filament
    protected function getDeleteAction(): Action
    {
        return parent::getDeleteAction()
            ->before(function ($record) {
                $this->handleFileDelete($record);
            });
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListImages::route('/'),
            'create' => Pages\CreateImage::route('/create'),
            'edit' => Pages\EditImage::route('/{record}/edit'),
        ];
    }
}
