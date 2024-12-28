<?php

namespace App\Filament\Pages;

use App\Models\Company2;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;

class EditCompany2 extends Page implements HasForms
{
    use InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.edit-company2';
    protected static ?string $model = Company2::class;

    // public Company2 $data ; 
    public  $data=[];
    public function mount(): void
    {
        $company = Company2::firstOrNew();

        $this->form->fill([
            'name' => $company->name,
            'logo' => $company->logo
        ]);
    }
   
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
                FileUpload::make('logo')
                    ->label('Company Logo')
                    ->disk('public')
                    ->directory('logos')
                    ->required(),
            ])
            ->statePath('data');
    } 

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        try {
            // $validatedSettings = $this->validate([
            //     'name' => 'required|min:5|max:150',
            //     'logo' => 'nullable',
            // ]);
            // dd($this->form->getState());
            $data = $this->form->getState();
            $company2 = Company2::firstOrNew([]);
            $company2->fill($data);
            $company2->save();

        } catch (Halt $exception) {
            return;
        }
        Notification::make() 
            ->success()
            ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))
            ->send(); 
    }
}
