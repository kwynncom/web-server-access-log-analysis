#include <stdio.h>

void main(void) {
    int i = 0;
    const int readLimit = 8000;
    char   ln[readLimit + 1];
    unsigned long long  fx = 0;
    unsigned long long  lx = 0;
    unsigned long long c64 = 0;

    while (fgets(ln, readLimit, stdin) != NULL) {
        lx = 0;
        for (i=0; c64 = (unsigned long long) ln[i]; i++) lx ^= c64 << (8 * (i % 8));
        fx ^= lx;
    }
    
    printf("%llu\n", fx);
}
