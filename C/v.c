#include <stdio.h>

void main(void) {
    int i = 0;
    const int  readLimit = 8000;
    char lnbuf[readLimit + 1];

    typedef signed long long XTYPE;
    XTYPE  fx = 0;
    XTYPE  lx = 0;
    XTYPE c64 = 0;

    while (fgets(lnbuf, readLimit, stdin) != NULL) {
        lx = 0;
        for (i=0; c64 = (XTYPE) lnbuf[i]; i++) lx ^= c64 << (8 * (i % 8));
        fx ^= lx;
    }
    
    printf("%lld\n", fx);
}
