<?php
namespace Kachkaev\PostgresHelperBundle\Model\Validator;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * Checks dataset component name validity

 * @author  "Alexander Kachkaev <alexander@kachkaev.ru>"
 *
 * @DI\Service("postgres_helper.validator.component_name")
 */

class ComponentNameValidator implements ValidatorInterface
{
    // XXX pattern now both matches names for components and views; consider revising if they get separated
    private $pattern = "/^_?[a-z]([a-z0-9]*(_([a-z0-9]+))?)*$/";
    
    public function isValid($value)
    {
        return (bool) preg_match($this->pattern, $value) && $value !== 'meta';
    }

    public function assertValid($value)
    {
        if (!$this->isValid($value)) {
            throw new \InvalidArgumentException(sprintf('%s is not a valid name for a component. It must be alphanumeric, lowercase, have not more than one underscore in a row and be not equal to "meta".', var_export($value, true)));
        }
    }
}