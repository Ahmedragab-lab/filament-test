<?php

namespace App\Filament\Pages;

use App\Models\SystemSetting;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
class CounterPage extends Page implements HasForms
{
    use InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.counter-page';
    protected static ?string $model = SystemSetting::class;
    
    public $company_name='';
    public $company_logo='';
    public $time_zone='UTC';
    public $date_format='Y-m-d';
    public $language='en';
    public $theme='light';
    public function mount(): void {
        $currentSettings = SystemSetting::first();
        if ($currentSettings) {
            $this->company_name = $currentSettings->company_name;
            $this->company_logo = $currentSettings->company_logo??[];
            $this->time_zone = $currentSettings->time_zone;
            $this->date_format = $currentSettings->date_format;
            $this->language = $currentSettings->language;
            $this->theme = $currentSettings->theme;
        }
    }
    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Company Information'))
                    ->schema([
                        Forms\Components\FileUpload::make('company_logo')
                            ->label('Company Logo')
                            ->image()
                            ->maxFiles(1) // Ensure only one file can be uploaded
                            ->getUploadedFileNameForStorageUsing(fn($file) => $file->store('logos'))
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

            ]);
    }

    public function saveChanges(){
        $validatedSettings = $this->validate([
            'company_name' => 'required|min:5|max:150',
            'company_logo' => 'nullable',
            'time_zone' => 'required',
            'date_format' => 'required',
            'language' => 'required',
            'theme' => 'required',
        ]);
        


        $systemSetting = SystemSetting::firstOrNew([]);
        $systemSetting->fill($validatedSettings);
        $systemSetting->save();

        session()->flash('message', __('Settings saved successfully.'));
    }
    public function restoreDefaults()
    {

        $this->company_name = 'Rubix';
        $this->company_logo ??= '';
        $this->time_zone = 'UTC';
        $this->date_format = 'Y-m-d';
        $this->language = 'en';
        $this->theme = 'light';

        $this->saveChanges();
    }
}
