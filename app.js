function parseValorMonetario(valor) {
    if (valor === null || valor === undefined || valor === '')
        return 0;
    const numero = parseFloat(valor.toString().replace(',', '.'));
    return isNaN(numero) ? 0 : numero;
}
function atualizarInterface(faturamento, total, filtrados) {
    const elFaturamento = document.getElementById('card-faturamento');
    if (elFaturamento)
        elFaturamento.innerText = `R$ ${faturamento.toFixed(2).replace('.', ',')}`;
    const elTotal = document.getElementById('card-total');
    if (elTotal)
        elTotal.innerText = total.toString();
    const elFiltrados = document.getElementById('card-filtrados');
    if (elFiltrados)
        elFiltrados.innerText = filtrados.toString();
}
function obterDentistaDestaque(dados) {
    if (dados.length === 0)
        return 'Nenhum';
    const contagem = {};
    dados.forEach((c) => {
        const dentista = c.dentista_nome || 'Não informado';
        contagem[dentista] = (contagem[dentista] || 0) + 1;
    });
    let maisAtendeu = '';
    let maxConsultas = 0;
    for (const [dentista, total] of Object.entries(contagem)) {
        if (total > maxConsultas) {
            maxConsultas = total;
            maisAtendeu = dentista;
        }
    }
    return `${maisAtendeu} (${maxConsultas} consultas)`;
}
async function carregarDashboard() {
    try {
        const resposta = await fetch('api.php');
        if (!resposta.ok)
            throw new Error(`Erro na requisição: ${resposta.status}`);
        const dados = await resposta.json();
        if (!Array.isArray(dados) || dados.length === 0) {
            atualizarInterface(0, 0, 0);
            return;
        }
        const valoresFormatados = dados.map((item) => {
            const valor = parseValorMonetario(item.valor_final ?? item.valor_base);
            return `R$ ${valor.toFixed(2).replace('.', ',')}`;
        });
        const maisCaros = dados.filter((item) => {
            return parseValorMonetario(item.valor_final ?? item.valor_base) >= 150;
        });
        const faturamento = dados.reduce((total, item) => {
            return total + parseValorMonetario(item.valor_final ?? item.valor_base);
        }, 0);
        const dentistaPop = obterDentistaDestaque(dados);
        console.log('Dentista Destaque:', dentistaPop);
        console.log('Valores formatados via .map():', valoresFormatados);
        atualizarInterface(faturamento, dados.length, maisCaros.length);
    }
    catch (erro) {
        console.error('Falha no Dashboard:', erro);
    }
}
document.addEventListener('DOMContentLoaded', carregarDashboard);
export {};
