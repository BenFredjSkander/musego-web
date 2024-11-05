<?php
declare(strict_types=1);

namespace App\Dto\Request;

use Phpro\ApiProblem\Exception\ApiProblemException;
use Phpro\ApiProblem\Http\ValidationApiProblem;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/**
 * Class to deserialize and validate each object that implements  @see BaseRequestInterface
 */
final class BaseRequest implements ParamConverterInterface
{
    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;

    /**
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     */
    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /**
     * @param Request $request
     * @param ParamConverter $configuration
     *
     * @return void
     * @throws ApiProblemException
     */
    public function apply(Request $request, ParamConverter $configuration): void
    {
        $body = $request->getContent();

        $obj = $this->serializer->deserialize($body, $configuration->getClass(), JsonEncoder::FORMAT);
        $violationList = $this->validator->validate($obj);
        $request->attributes->set('_violations', $violationList);
        $request->attributes->set($configuration->getName(), $obj);
        if (count($violationList) > 0) {
            throw new ApiProblemException(new ValidationApiProblem($violationList));
        }
    }

    /**
     * @param ParamConverter $configuration
     *
     * @return bool
     */
    public function supports(ParamConverter $configuration): bool
    {
        $class = $configuration->getClass();
        if (!is_string($class)) {
            return false;
        }

        return in_array(BaseRequestInterface::class, class_implements($class));
    }
}
