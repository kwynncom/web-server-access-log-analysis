#include <stdio.h>
#include <stdlib.h>

void main(void) {
    char ln[8000];
    unsigned long long fx = 0;
    unsigned long long lx = 0;
    unsigned long long  i = 0;
    unsigned long long sh = 0;
    unsigned long long shd = 0;
    unsigned long long c64 = 0;
    while (fgets(ln, 8000, stdin) != NULL) {
        lx = 0;
        for (i=0; c64 = (unsigned long long) ln[i]; i++) {
            sh = (8 * (i % 8));
            shd = c64 << sh;
            lx ^= shd;
        }
        fx ^= lx;
    }
    
    printf("%llu\n", fx);
}
