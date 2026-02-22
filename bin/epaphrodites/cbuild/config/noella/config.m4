PHP_ARG_ENABLE(noella, whether to enable the Noella extension,
[  --enable-noella      Enable Noella extension support])

if test "$PHP_NOELLA" != "no"; then
    AC_DEFINE(HAVE_NOELLA, 1, [Have Noella extension])
    PHP_NEW_EXTENSION(noella, ../cFunctions/noella.c, $ext_shared)
    PHP_ADD_INCLUDE([../config/noella])
fi