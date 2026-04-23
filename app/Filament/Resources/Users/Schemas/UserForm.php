<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama')
                    ->placeholder('Masukkan nama lengkap')
                    ->required()
                    ->maxLength(100)
                    ->validationMessages([
                        'required' => 'Nama wajib diisi',
                        'max'      => 'Nama maksimal 100 karakter',
                    ]),

                TextInput::make('email')
                    ->label('Email')
                    ->placeholder('contoh@email.com')
                    ->email()
                    ->required()
                    ->unique(
                        table: 'users',
                        column: 'email',
                        ignoreRecord: true // ← agar tidak error saat edit
                    )
                    ->maxLength(255)
                    ->validationMessages([
                        'required' => 'Email wajib diisi',
                        'email'    => 'Format email tidak valid',
                        'unique'   => 'Email sudah digunakan',
                        'max'      => 'Email maksimal 255 karakter',
                    ]),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->required(fn($record) => $record === null) // ← wajib saat create, opsional saat edit
                    ->minLength(8)
                    ->dehydrated(fn($state) => filled($state)) // ← hanya simpan jika diisi
                    ->dehydrateStateUsing(fn($state) => bcrypt($state)) // ← otomatis hash
                    ->placeholder(fn($record) => $record ? 'Kosongkan jika tidak ingin mengubah password' : 'Minimal 8 karakter')
                    ->validationMessages([
                        'required' => 'Password wajib diisi',
                        'min'      => 'Password minimal 8 karakter',
                    ])
                    ->helperText('Password min 8 karakter'),

                TextInput::make('password_confirmation')
                    ->label('Konfirmasi Password')
                    ->password()
                    ->revealable()
                    ->required(fn($record) => $record === null)
                    ->same('password') // ← harus sama dengan password
                    ->dehydrated(false) // ← tidak disimpan ke DB
                    ->placeholder('Ulangi password')
                    ->validationMessages([
                        'required' => 'Konfirmasi password wajib diisi',
                        'same'     => 'Konfirmasi password tidak cocok',
                    ]),

                Select::make('role')
                    ->label('Role')
                    ->options([
                        'administrator' => 'Administrator',
                    ])
                    ->default('administrator')
                    ->required()
                    ->native(false)
                    ->validationMessages([
                        'required' => 'Role wajib dipilih',
                    ]),
            ]);
    }
}
