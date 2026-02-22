PHP_ARG_ENABLE(shlomo, whether to enable the Shlomo extension,
[  --enable-shlomo   Enable Shlomo extension support])

if test "$PHP_SHLOMO" != "no"; then
    AC_DEFINE(HAVE_SHLOMO, 1, [Have Shlomo extension])
    PHP_NEW_EXTENSION(shlomo, ../cFunctions/shlomo.c, $ext_shared)
    PHP_ADD_INCLUDE([../config/shlomo])
fi