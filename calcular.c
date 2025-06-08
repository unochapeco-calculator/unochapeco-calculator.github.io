#include <stdio.h>
#include <stdlib.h>
#include <string.h>

#define MAX 100

typedef struct {
    char tipo[20];
    float nota;
    float peso_percentual;
} Avaliacao;

float calcularMedia(const char *data) {
    Avaliacao avaliacoes[MAX];
    int quantidade = 0;
    float soma = 0.0, totalPesos = 0.0;

    char *token = strtok((char *)data, "&");
    while (token != NULL && quantidade < MAX) {
        sscanf(token, "tipo%d=%[^&]", &quantidade, avaliacoes[quantidade].tipo);
        token = strtok(NULL, "&");
        sscanf(token, "nota%d=%f", &quantidade, &avaliacoes[quantidade].nota);
        token = strtok(NULL, "&");
        sscanf(token, "peso%d=%f", &quantidade, &avaliacoes[quantidade].peso_percentual);
        quantidade++;
        token = strtok(NULL, "&");
    }

    for (int i = 0; i < quantidade; i++) {
        float peso = avaliacoes[i].peso_percentual / 100.0;
        soma += avaliacoes[i].nota * peso;
        totalPesos += peso;
    }

    if (totalPesos == 0) return -1.0;
    return soma;
}

int main() {
    char *len_str = getenv("CONTENT_LENGTH");
    int len = len_str ? atoi(len_str) : 0;

    char post_data[1024] = {0};
    fread(post_data, 1, len, stdin);

    float media = calcularMedia(post_data);

    printf("Content-Type: text/html\n\n");
    printf("<html><body>");
    if (media < 0) {
        printf("<p>Erro: pesos inválidos.</p>");
    } else {
        printf("<p>Média final: %.2f</p>", media);
    }
    printf("</body></html>");
    return 0;
}
