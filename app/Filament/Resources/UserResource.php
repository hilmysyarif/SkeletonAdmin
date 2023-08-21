<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Filament\Infolists\Components\TextEntry;
use Tapp\FilamentAuditing\RelationManagers\AuditsRelationManager;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?string $recordTitleAttribute = 'name';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(ucwords(__('validation.attributes.name')))
                            ->required(),
                        Forms\Components\TextInput::make('username')
                            ->required()
                            ->unique(table: static::$model, ignorable: fn ($record) => $record)
                            ->label(ucwords(__('validation.attributes.username'))),
                        Forms\Components\TextInput::make('email')
                            ->required()
                            ->email("Email")
                            ->unique(table: static::$model, ignorable: fn ($record) => $record)
                            ->label(ucwords(__('validation.attributes.email'))),
                        Forms\Components\TextInput::make('password')
                            ->same('passwordConfirmation')
                            ->password()
                            ->maxLength(255)
                            ->required(fn ($component, $get, $livewire, $model, $record, $set, $state) => $record === null)
                            ->dehydrateStateUsing(fn ($state) => !empty($state) ? Hash::make($state) : '')
                            ->label(ucwords(__('validation.attributes.password'))),
                        Forms\Components\TextInput::make('passwordConfirmation')
                            ->password()
                            ->dehydrated(false)
                            ->maxLength(255)
                            ->label(ucwords(__('validation.attributes.password_confirmation'))),
                        Forms\Components\Select::make('roles')
                            ->searchable()
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->preload()
                            ->label(ucwords(__('validation.attributes.role'))),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->label('ID'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label(ucwords(__('validation.attributes.name'))),
                Tables\Columns\TextColumn::make('username')
                    ->searchable()
                    ->sortable()
                    ->label(ucwords(__('validation.attributes.username'))),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->label(ucwords(__('validation.attributes.email'))),
                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->label(ucwords(__('validation.attributes.role'))),
                Tables\Columns\ToggleColumn::make('status')
                    ->tooltip('Active/Inactive')
                    ->label(ucwords(__('validation.attributes.status'))),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('Y-m-d H:i')
                    ->label(ucwords(__('validation.attributes.created_at'))),
            ])
            ->filters([
                Tables\Filters\Filter::make('verified')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
            AuditsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('username'),
                        TextEntry::make('email'),
                        TextEntry::make('email_verified_at'),
                        IconEntry::make('status')
                            ->boolean()
                            ->size(IconEntry\IconEntrySize::Medium),
                    ])->columns(2)
            ]);
    }
}
