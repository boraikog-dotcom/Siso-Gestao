function calcularDesconto(valor, porcentagem) {
    if (valor < 0 || porcentagem < 0) {
        return 0;
    }
    let desconto = valor * (porcentagem / 100);
    return valor - desconto;
}

let elementosPreco = document.querySelectorAll('.preco-final');

elementosPreco.forEach(function (item) {
    let valorAntigo = parseFloat(item.textContent);
    let valorNovo = calcularDesconto(valorAntigo, 10);
    item.textContent = valorNovo.toFixed(2);
});