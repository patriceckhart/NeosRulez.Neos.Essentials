<?php
namespace NeosRulez\Neos\Essentials\Controller;

/*
 * This file is part of the NeosRulez.Neos.Essentials package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Flow\Property\TypeConverter\CurrencyConverter;
use Neos\Flow\Property\TypeConverter\DateTimeConverter;
use Neos\Flow\Mvc\View\JsonView;
use Neos\Fusion\View\FusionView;
use NeosRulez\Neos\Essentials\Service\MailService;
use NeosRulez\Neos\Essentials\Service\UserService;

#[Flow\Scope("singleton")]
abstract class AbstractActionController extends ActionController
{

    /**
     * @var string
     */
    protected $defaultViewObjectName = JsonView::class;

    /**
     * @var array
     */
    protected $viewFormatToObjectNameMap = [
        'html' => FusionView::class,
        'json' => JsonView::class,
    ];

    #[Flow\Inject]
    protected MailService $mailService;

    #[Flow\Inject]
    protected UserService $userService;

    /**
     * @return void
     */
    public function redirectToSource(): void
    {
        $this->redirectToUri($_SERVER['HTTP_REFERER']);
    }

    /**
     * @param string $argument
     * @param string $property
     * @return void
     */
    protected function convertDate(string $argument, string $property): void
    {
        $this->arguments[$argument]->getPropertyMappingConfiguration()->forProperty($property)->setTypeConverterOption(DateTimeConverter::class, DateTimeConverter::CONFIGURATION_DATE_FORMAT, 'Y-m-d\TH:i');
    }

    /**
     * @param string $argument
     * @param string|null $property
     * @return void
     */
    protected function convertFloat(string $argument, string $property = null): void
    {
        $currencyTypeConverter = $this->objectManager->get(CurrencyConverter::class);
        if ($property !== null) {
            $this->arguments[$argument]->getPropertyMappingConfiguration()->forProperty($property)->setTypeConverter($currencyTypeConverter);
            $this->arguments[$argument]->getPropertyMappingConfiguration()->forProperty($property)->setTypeConverterOption(CurrencyConverter::class, 'locale', true);
        } else {
            $this->arguments[$argument]->getPropertyMappingConfiguration()->setTypeConverter($currencyTypeConverter);
            $this->arguments[$argument]->getPropertyMappingConfiguration()->setTypeConverterOption(CurrencyConverter::class, 'locale', true);
        }
    }

    /**
     * @param string $argument
     * @param string $property
     * @return void
     */
    protected function convertTime(string $argument, string $property): void
    {
        $this->arguments[$argument]->getPropertyMappingConfiguration()->forProperty($property)->setTypeConverterOption(DateTimeConverter::class, DateTimeConverter::CONFIGURATION_DATE_FORMAT, 'H:i');
    }

}
