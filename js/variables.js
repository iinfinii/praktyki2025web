// GLOBAL VARIABLES

// 1. REGEX 
const MAIL_REGEX = /^((?!\.)[\w\-_.]*[^.])@(\w+)(\.\w+(\.\w+)?[^.\W])$/;
const PHONE_NUMBER_REGEX = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/;
const LETTERS_ONLY_REGEX = /^[a-zA-ZąćęłńóśźżĄĘŁŃÓŚŹŻ]+$/;
const SURNAME_REGEX = /^[a-zA-ZąćęłńóśźżĄĘŁŃÓŚŹŻ\- ]+$/;
const NO_SPECIAL_SYMBOLS_REGEX = /^[a-zA-Z0-9\s!@#$%^&*()_+\-=\[\]{}|:\\;,.<>/?]*$/;

// 2. CONST. LENGTHS
const MAX_SIZE_MAIL = 64;
const MAX_SIZE_PHONE_NUMBER = 20;
const MAX_SIZE_FIRST_NAME = 32;
const MAX_SIZE_LAST_NAME = 32;
const MAX_SIZE_PASSWORD = 64;

