<?php

/**
 * PHP Form Validator
 *
 * PHP Form Validator (Under development)
 *
 * @author       Alejandro Perez Martin
 * @copyright    Copyright (c) 2014, Alejandro Perez Martin
 * @license      GNU General Public License (GPL), https://www.gnu.org/licenses/gpl.html
 * @version      1.0.0
 * @url          http://linkedin.com/in/aleperez92
 * @repository   http://github.com/alejandroperezmartin/php-form-validator
 *
 */
class FormValidator
{

    protected $errors = array();
    protected $fields = array();
    protected $values = array();

    /**
     * Form Validator constructor
     *
     * @constructor
     *
     * @param array $fields
     * @param array $fieldsValue
     */
    function __construct( $fields, $fieldsValue )
    {
        $this->fields = $fields;
        $this->values = $fieldsValue;
    }

    /**
     * Returns an array containing all the fields and its rules
     *
     * @return array Array of each field name and its rules
     */
    function getFields()
    {
        return $this->fields;
    }

    /**
     * Returns the field values in an array
     *
     * @return array Array of field values
     */
    function getValues()
    {
        return $this->values;
    }

    /**
     * Returns an array containing all the fields errors
     *
     * @return array Array of field name and error message of that field
     */
    function getErrors()
    {
        return $this->errors;
    }

    /**
     * Adds an element to the 'errors' array that contains the field name and an error message
     *
     * @param String $fieldName Name of the field
     * @param String $message Error message
     */
    function addError( $fieldName, $message )
    {
        $this->errors[ $fieldName ] = $message;
    }

    /**
     * Returns true if form is valid (all its fields are valid), false otherwise
     *
     * @return boolean True if form is valid, otherwise false
     */
    function validateForm()
    {
        foreach ( $this->fields as $field )
        {
            if ( !$this->validateField( $field ) )
            {
                continue;
            }
        }
        return empty( $this->errors );
    }

    /**
     * Returns true if a field is valid, false otherwise
     *
     * @param array $field Array that follows the next structure ['name' => 'fieldName', 'rules' => 'fieldRules']
     * @return boolean True if field is valid, false otherwise
     */
    private function validateField( $field )
    {
        $rules      = is_array( $field[ 'rules' ] ) ? $field[ 'rules' ] : array($field[ 'rules' ]); // get rules as an array
        $fieldValue = $this->values[ $field[ 'name' ] ];

        // If field is not required and is empty, it's valid
        if ( !in_array( 'required', $rules ) && empty( $fieldValue ) )
        {
            return true; // field is valid
        }

        foreach ( $rules as $rule )
        {
            $argumentPosition = strpos( $rule, '=' ); // get argument position

            // Rule has arguments
            if ( $argumentPosition )
            {
                $ruleName = substr( $rule, 0, $argumentPosition );
                $arg      = substr( $rule, $argumentPosition + 1, strlen( $rule ) );

                if ( !$this->$ruleName( $field[ 'name' ], $fieldValue, $arg ) )
                {
                    return false; // invalid field, stop checking next rules
                }
            }
            // Rule without arguments
            else
            {
                if ( !$this->$rule( $field[ 'name' ], $fieldValue ) )
                {
                    return false; // invalid field, stop checking next rules
                }
            }
        }
        return true; // field is valid
    }

    /**
     * Returns true if a field is not empty, otherwise false
     *
     * @param String $fieldName Field name
     * @param String $value Value of the field $fieldName
     * @return boolean True if field is not empty, false otherwise
     */
    private function required( $fieldName, $value )
    {
        if ( empty( $value ) )
        {
            $this->addError( $fieldName, 'Este campo es obligatorio' );
            return false;
        }
        return true;
    }

    /**
     * Returns true if a field contains only alphabetic characters , otherwise false
     *
     * @param String $fieldName Field name
     * @param String $value Value of the field $fieldName
     * @return boolean True if a field matches the regular expression pattern, otherwise false
     */
    private function alphabetic( $fieldName, $value )
    {
        if ( !preg_match( "/^[A-z 'áéíóúñçÁÉÍÓÚÑÇ]+$/", $value ) )
        {
            $this->addError( $fieldName, 'Please enter a valid name' );
            return false;
        }
        return true;
    }

    /**
     * Returns true if a field contains only alphanumeric characters, otherwise false
     *
     * @param String $fieldName Field name
     * @param String $value Value of the field $fieldName
     * @return boolean True if a field matches the regular expression pattern, otherwise false
     */
    private function alphanumeric( $fieldName, $value )
    {
        if ( !preg_match( "/^[A-z0-9- 'áéíóúñçÁÉÍÓÚÑÇ]+$/", $value ) )
        {
            $this->addError( $fieldName, 'This field only allows alphanumeric characters' );
            return false;
        }
        return true;
    }

    /**
     * Returns true if a field is a valid email, otherwise false
     *
     * @param String $fieldName Field name
     * @param String $value Value of the field $fieldName
     * @return boolean True if a field matches the regular expression pattern, otherwise false
     */
    private function email( $fieldName, $value )
    {
        if ( !preg_match( "/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/", $value ) )
        {
            $this->addError( $fieldName, 'Please enter a valid email' );
            return false;
        }
        return true;
    }

    /**
     * Returns true if a field contains only integers, otherwise false
     *
     * @param String $fieldName Field name
     * @param String $value Value of the field $fieldName
     * @return boolean True if a field matches the regular expression pattern, otherwise false
     */
    private function intnumber( $fieldName, $value )
    {
        if ( !preg_match( "/^\-?[0-9]+$/", $value ) )
        {
            $this->addError( $fieldName, 'This field only allows integers' );
            return false;
        }
        return true;
    }

    /**
     * Returns true if a field is equal to a given argument, otherwise false
     *
     * @param String $fieldName Field name
     * @param String $value Value of the field $fieldName
     * @param String $var Value used to compare $value
     * @return boolean True if the field value ($value) is equal to $var, otherwise false
     */
    private function equal_to( $fieldName, $value, $var )
    {
        if ( $value != $var )
        {
            $this->addError( $fieldName, 'This field must be equal to ' . $var );
            return false;
        }
        return true;
    }

    /**
     * Returns true if a field length is equal to a given argument, otherwise false
     *
     * @param String $fieldName Field name
     * @param String $value Value of the field $fieldName
     * @param String $var Value used to compare $value
     * @return boolean True if the field length is equal to $var, otherwise false
     */
    private function exact_length( $fieldName, $value, $var )
    {
        if ( strlen( $value ) != $var )
        {
            $this->addError( $fieldName, 'This fields must be exactly ' . $var . ' characters in length' );
            return false;
        }
        return true;
    }

    /**
     * Returns true if a field is greater than the given argument, otherwise false
     *
     * @param String $fieldName Field name
     * @param String $value Value of the field $fieldName
     * @param String $var Value used to compare $value
     * @return boolean True if the field value is greater than $var, otherwise false
     */
    private function greater_than( $fieldName, $value, $var )
    {
        if ( $value <= $var )
        {
            $this->addError( $fieldName, 'This field must be greater than ' . $var );
            return false;
        }
        return true;
    }

    /**
     * Returns true if a field is lower than the given argument, otherwise false
     *
     * @param String $fieldName Field name
     * @param String $value Value of the field $fieldName
     * @param String $var Value used to compare $value
     * @return boolean True if the field value is lower than $var, otherwise false
     */
    private function less_than( $fieldName, $value, $var )
    {
        if ( $value >= $var )
        {
            $this->addError( $fieldName, 'This field must be less than ' . $var );
            return false;
        }
        return true;
    }

    /**
     * Returns true if a field length is at least equal to a given argument, otherwise false
     *
     * @param String $fieldName Field name
     * @param String $value Value of the field $fieldName
     * @param String $var Value used to compare $value
     * @return boolean True if the field length is equal or greater than $var, otherwise false
     */
    private function min_length( $fieldName, $value, $var )
    {
        if ( strlen( $value ) < $var )
        {
            $this->addError( $fieldName, 'This fields must be at least ' . $var . ' characters in length' );
            return false;
        }
        return true;
    }

    /**
     * Returns true if a field length is lower than given argument, otherwise false
     *
     * @param String $fieldName Field name
     * @param String $value Value of the field $fieldName
     * @param String $var Value used to compare $value
     * @return boolean True if the field length is lower than $var, otherwise false
     */
    private function max_length( $fieldName, $value, $var )
    {
        if ( strlen( $value ) > $var )
        {
            $this->addError( $fieldName, 'This fields must be at most ' . $var . ' characters in length' );
            return false;
        }
        return true;
    }

    /**
     * Returns true if a field contains a valid name, otherwise false
     *
     * @param String $fieldName Field name
     * @param String $value Value of the field $fieldName
     * @return boolean True if a field matches the regular expression pattern, otherwise false
     */
    private function name( $fieldName, $value )
    {
        if ( !preg_match( "/^([a-z]+[ ]?){1,}$/", $value ) )
        {
            $this->addError( $fieldName, 'Please enter a valid name' );
            return false;
        }
        return true;
    }

    /**
     * Returns true if a field is a valid Spanish DNI, otherwise false
     *
     * @param String $fieldName Field name
     * @param String $value Value of the field $fieldName
     * @return boolean True if a field matches the regular expression pattern, otherwise false
     */
    private function spanish_dni( $fieldName, $value )
    {
        if ( preg_match( "/^[0-9]{8}[a-zA-Z]{1}$/", $value ) )
        {
            $letters = 'TRWAGMYFPDXBNJZSQVHLCKET';

            if ( $letters[ substr( $value, 0, 8 ) % 23 ] === strtoupper( $value[ strlen( $value ) - 1 ] ) )
            {
                return true;
            }
        }
        $this->addError( $fieldName, 'Please enter a valid DNI' );
        return false;
    }

    /**
     * Returns true if a field is a valid user name (alphanumeric string with no whitespaces), otherwise false
     *
     * @param String $fieldName Field name
     * @param String $value Value of the field $fieldName
     * @return boolean True if a field matches the regular expression pattern, otherwise false
     */
    private function username( $fieldName, $value )
    {
        if ( !preg_match( "/^[a-zA-Z0-9_-]+$/", $value ) )
        {
            $this->addError( $fieldName, 'This fields must be at most ' . $var . ' characters in length' );
            return false;
        }
        return true;
    }
}

?>
