<?php

namespace App\Filament\App\Resources\Customers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('textphones')
                    ->searchable(),
                TextColumn::make('notifyemails')
                    ->searchable(),
                TextColumn::make('billingemail')
                    ->searchable(),
                TextColumn::make('status')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('qid')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('stripe_customer_id')
                    ->searchable(),
                IconColumn::make('e_billing')
                    ->boolean(),
                IconColumn::make('yearly_billing')
                    ->boolean(),
                TextColumn::make('balance')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('adjustment')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('firstname')
                    ->searchable(),
                TextColumn::make('middlename')
                    ->searchable(),
                TextColumn::make('lastname')
                    ->searchable(),
                TextColumn::make('address')
                    ->searchable(),
                TextColumn::make('city')
                    ->searchable(),
                TextColumn::make('state')
                    ->searchable(),
                TextColumn::make('zip')
                    ->searchable(),
                TextColumn::make('lastlogin')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
