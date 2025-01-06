<?php

namespace App\Filament\Pages;

use App\Models\SystemSetting;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Notifications\Notification;

class Settings5 extends Page implements HasForms
{
    use InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.settings5';

    protected static ?string $model = SystemSetting::class;

    public  $data=[];
    public function mount(): void
    {
        $currentSettings = SystemSetting::firstOrNew();
        $this->form->fill([
            'company_name' => $currentSettings->company_name,
            'company_logo' => $currentSettings->company_logo,
            'time_zone' => $currentSettings->time_zone,
            'date_format' => $currentSettings->date_format,
            'language' => $currentSettings->language,
            'theme' => $currentSettings->theme,
        ]);
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Company Information'))
                    ->schema([
                        FileUpload::make('company_logo')
                            ->label('Company Logo')
                            ->disk('public')
                            ->directory('logos'),
                        Forms\Components\TextInput::make('company_name')
                            ->label('Company Name')
                            ->live()
                            ->validationAttribute('min:5|max:150')
                            ->required()
                    ])
                    ->icon('heroicon-o-check-circle'),
                Forms\Components\Section::make(__('Localization'))
                    ->schema([
                        Forms\Components\Select::make('time_zone')
                            ->label('Time Zone')
                            ->options(timezone_identifiers_list())
                            ->required()
                            ->searchable(),
                        Forms\Components\Select::make('date_format')
                            ->searchable()
                            ->options([
                                'Y-m-d' => 'YYYY/MM/DD',
                                'd-m-Y' => 'DD/MM/YYYY',
                            ])
                            ->required(),
                        Forms\Components\Select::make('language')
                            ->label('Language')
                            ->searchable()
                            ->options([
                                'en' => 'English',
                                'ar' => 'Arabic',
                            ])
                            ->required(),
                    ])
                    ->icon('heroicon-o-check-circle'),
                Forms\Components\Section::make(__('Appearance & Themes'))
                    ->schema([
                        Forms\Components\Select::make('theme')
                            ->label('Theme')
                            ->searchable()
                            ->options([
                                'auto' => 'Automatic',
                                'light' => 'Light',
                                'dark' => 'Dark',
                            ])
                            ->default('auto')
                            ->required()
                    ])
                    ->icon('heroicon-o-check-circle'),
                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make(__('Save Changes'))
                        ->icon('heroicon-o-check-circle')
                        ->action('saveChanges')
                        ->color('primary'),
                    Forms\Components\Actions\Action::make(__('Restore defaults'))
                        ->icon('heroicon-s-arrow-uturn-left')
                        ->action('restoreDefaults')
                        ->requiresConfirmation()
                        ->modalHeading('Heading')
                        ->modalDescription('Are you sure you\'d like to restore defaults? This cannot be undone.')
                        ->color('warning')

                ])->alignEnd(),

            ])
            ->statePath('data');
    }

    public function saveChanges()
    {
        // dd($this->form->getState());
        $data = $this->form->getState();
        $systemSetting = SystemSetting::firstOrNew([]);
        $systemSetting->fill($data);
        $systemSetting->save();

        Notification::make()
            ->success()
            ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))
            ->send();
    }
    public function restoreDefaults(): void
    {
        $this->form->fill([
            'company_name' => 'Rubix',
            'company_logo' => null,
            'time_zone' => 'UTC',
            'date_format' => 'Y-m-d',
            'language' => 'en',
            'theme' => 'light',
        ]);

        $systemSetting = SystemSetting::firstOrNew([]);
        $systemSetting->fill($this->form->getState());
        $systemSetting->save();

        Notification::make()
            ->success()
            ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))
            ->send();
    }

}
