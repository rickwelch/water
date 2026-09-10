<?php

namespace App\Filament\App\Resources\Customers\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('textphones')
                    ->tel(),
                TextInput::make('notifyemails')
                    ->email(),
                TextInput::make('billingemail')
                    ->email(),
                TextInput::make('status')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('qid')
                    ->numeric(),
                TextInput::make('stripe_customer_id'),
                Toggle::make('e_billing')
                    ->required(),
                Toggle::make('yearly_billing')
                    ->required(),
                TextInput::make('balance')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('adjustment')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('firstname')
                    ->required(),
                TextInput::make('middlename'),
                TextInput::make('lastname')
                    ->required(),
                TextInput::make('address'),
                TextInput::make('city'),
                TextInput::make('state'),
                TextInput::make('zip'),
                DateTimePicker::make('lastlogin'),
                TextInput::make('metadata'),
            ]);
    }
}
