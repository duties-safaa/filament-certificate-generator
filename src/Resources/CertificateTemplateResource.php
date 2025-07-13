<?php

namespace HusamTariq\FilamentCertificateGenerator\Resources;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use HusamTariq\FilamentCertificateGenerator\Actions\Table\DownloadCertificateAction;
use HusamTariq\FilamentCertificateGenerator\Components\CertificateEditor;
use HusamTariq\FilamentCertificateGenerator\Concerns\HasCertificateTypes;
use HusamTariq\FilamentCertificateGenerator\FilamentCertificateGeneratorPlugin;
use HusamTariq\FilamentCertificateGenerator\Models\CertificateTemplate;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class CertificateTemplateResource extends Resource
{
    protected static ?string $model = CertificateTemplate::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public function __construct(
        protected HasCertificateTypes $certificateTypeService
    ) {}

    public static function getPluralLabel(): ?string
    {
        return __("filament-certificate-generator::certificate-generator.resource.plural");
    }

    public static function getModelLabel(): string
    {
        return __("filament-certificate-generator::certificate-generator.resource.singular");
    }

    public static function getNavigationIcon(): string|Htmlable|null
    {
        return 'certificate-icon';
    }

    public static function form(Form $form): Form
    {
        $typeService = app(HasCertificateTypes::class);

        return $form
            ->columns(1)
            ->schema([
                TextInput::make("name")
                    ->required()
                    ->label(__("filament-certificate-generator::certificate-generator.resource.certificate-name")),
                FileUpload::make("image")
                    ->image()
                    ->required()
                    ->directory('certificate-templates')
                    ->label(__("filament-certificate-generator::certificate-generator.resource.certificate-image")),
                FileUpload::make('font')
                    ->label(__("filament-certificate-generator::certificate-generator.resource.certificate-font"))
                    ->directory('certificate-fonts')
                    ->preserveFilenames()
                    ->acceptedFileTypes(['application/octet-stream', 'application/x-font-ttf', 'application/x-font-truetype', 'font/ttf']),
                Select::make('type')
                    ->required()
                    ->options($typeService->getTypeOptions())
                    ->default(config('certificate-generator.default_type', 'qualification'))
                    ->label(__('filament-certificate-generator::certificate-generator.resource.certificate-type')),
                Toggle::make('default')
                    ->label(__("filament-certificate-generator::certificate-generator.resource.certificate-default"))
                    ->afterStateUpdated(function ($state, $set) {
                        if ($state) {
                            $set('default', true);
                        }
                    })
            ]);
    }

    public static function table(Table $table): Table
    {
        $typeService = app(HasCertificateTypes::class);

        return $table
            ->columns([
                Tables\Columns\ImageColumn::make("image")
                    ->height(100)
                    ->label(__("filament-certificate-generator::certificate-generator.resource.certificate-image")),
                Tables\Columns\TextColumn::make("name")
                    ->searchable()
                    ->sortable()
                    ->label(__("filament-certificate-generator::certificate-generator.resource.certificate-name")),
                Tables\Columns\TextColumn::make('type')
                    ->formatStateUsing(fn ($state) => $typeService->getTypeDisplay($state))
                    ->badge()
                    ->color(fn ($state) => $typeService->getTypeColor($state))
                    ->icon(fn ($state) => $typeService->getTypeIcon($state))
                    ->searchable()
                    ->sortable()
                    ->label(__('filament-certificate-generator::certificate-generator.resource.certificate-type')),
                Tables\Columns\IconColumn::make('default')
                    ->label(__("filament-certificate-generator::certificate-generator.resource.certificate-default"))
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(fn () => app(HasCertificateTypes::class)->getTypeOptions())
                    ->label(__('filament-certificate-generator::certificate-generator.resource.certificate-type')),
                Tables\Filters\TernaryFilter::make('default')
                    ->label(__("filament-certificate-generator::certificate-generator.resource.certificate-default"))
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\EditAction::make("editor")->label("editor")->form([
                    TextInput::make("name")->required(),
                    CertificateEditor::make("data")->label(__("filament-certificate-generator::certificate-generator.resource.certificate-data"))
                        ->imageURL(fn($record) => Storage::disk("public")->url($record->image))
                        ->width(850)
                        ->options(
                            FilamentCertificateGeneratorPlugin::get()->getEditorOptions()
                        ),
                ])->label(__('filament-certificate-generator::certificate-generator.actions.editor')),


                DownloadCertificateAction::make()->certificateName(fn($record) => $record?->name),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->when(
                filament('filament-certificate-generator')->hasAuthorScope(),
                fn ($query) => $query->mine()
            )
            ->orderBy("created_at", "desc");
    }

    public static function getPages(): array
    {
        return [
            'index' => \HusamTariq\FilamentCertificateGenerator\Resources\CertificateTemplateResource\Pages\ManageCertificateTemplates::route('/'),
        ];
    }
}
