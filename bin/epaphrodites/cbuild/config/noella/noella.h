#ifndef PHP_NOELLA_H
#define PHP_NOELLA_H

#define NOELLA_VERSION "0.1"
#define PHP_NOELLA_EXTNAME "noella"

extern zend_module_entry noella_module_entry;
#define phpext_noella_ptr &noella_module_entry

PHP_FUNCTION(hello_noella);

#endif /* PHP_NOELLA_H */