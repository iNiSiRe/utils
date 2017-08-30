<?php

namespace PrivateDev\Utils\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class IntegerOrEmptyTransformer implements DataTransformerInterface
{
    /**
     * @inheritdoc
     */
    public function transform($value)
    {
        return $value;
    }

    /**
     * @inheritdoc
     */
    public function reverseTransform($value)
    {
        if ($value === "") {
            $data = null;
        } elseif ($result = $this->validateIntegerValue($value)) {
            $data = $result;
        } else {
            throw new TransformationFailedException();
        }

        return $data;
    }

    /**
     * @param $value
     *
     * @return false|float|int|mixed
     */
    private function validateIntegerValue($value)
    {
        $position = 0;
        $formatter = new \NumberFormatter(\Locale::getDefault(), \NumberFormatter::DECIMAL);
        $decSep = $formatter->getSymbol(\NumberFormatter::DECIMAL_SEPARATOR_SYMBOL);

        if (false !== strpos($value, $decSep)) {
            $type = \NumberFormatter::TYPE_DOUBLE;
        } else {
            $type = PHP_INT_SIZE === 8
                ? \NumberFormatter::TYPE_INT64
                : \NumberFormatter::TYPE_INT32;
        }

        $result = $formatter->parse($value, $type, $position);

        if (intl_is_failure($formatter->getErrorCode())) {
            throw new TransformationFailedException($formatter->getErrorMessage());
        }

        if ($result >= PHP_INT_MAX || $result <= -PHP_INT_MAX) {
            throw new TransformationFailedException('I don\'t have a clear idea what infinity looks like');
        }

        if (is_int($result) && $result === (int) $float = (float) $result) {
            $result = $float;
        }

        if (false !== $encoding = mb_detect_encoding($value, null, true)) {
            $length = mb_strlen($value, $encoding);
            $remainder = mb_substr($value, $position, $length, $encoding);
        } else {
            $length = strlen($value);
            $remainder = substr($value, $position, $length);
        }

        // After parsing, position holds the index of the character where the
        // parsing stopped
        if ($position < $length) {
            // Check if there are unrecognized characters at the end of the
            // number (excluding whitespace characters)
            $remainder = trim($remainder, " \t\n\r\0\x0b\xc2\xa0");

            if ('' !== $remainder) {
                throw new TransformationFailedException(
                    sprintf('The number contains unrecognized characters: "%s"', $remainder)
                );
            }
        }

        return $result;
    }
}
