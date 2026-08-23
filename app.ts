interface Consulta {
    id_consulta: number;
    paciente_nome: string;
    dentista_nome: string;
    nome_procedimento: string;
    valor_final: string | number | null | undefined;
}

async function carregarDashboard(): Promise<void> {
    try {
        const resposta = await fetch('api.php');
        if (!resposta.ok) {
            throw new Error(`Erro na requisição: ${resposta.status} - ${resposta.statusText}`);
        }

        const dados: Consulta[] = await resposta.json();
        if (!Array.isArray(dados) || dados.length === 0) {
            console.warn('Nenhuma consulta encontrada para processar.');
            atualizarInterface(0, 0, 0);
            return;
        }
        const faturamento = dados.reduce((total: number, item: Consulta) => {
            const valorTratado = parseValorMonetario(item.valor_final);
            return total + valorTratado;
        }, 0);
        const maisCaros = dados.filter((item: Consulta) => {
            const valor = parseValorMonetario(item.valor_final);
            return valor >= 150;
        });
        atualizarInterface(faturamento, dados.length, maisCaros.length);

    } catch (erro) {
        console.error('Falha no processamento do Dashboard:', erro);
    }
}

function parseValorMonetario(valor: string | number | null | undefined): number {
    if (valor === null || valor === undefined || valor === '') {
        return 0;
    }
    const stringLimpa = valor.toString().replace(',', '.');
    const numero = parseFloat(stringLimpa);

    return isNaN(numero) ? 0 : numero;
}

function atualizarInterface(faturamento: number, total: number, filtrados: number): void {
    const elFaturamento = document.getElementById('card-faturamento');
    if (elFaturamento) elFaturamento.innerText = `R$ ${faturamento.toFixed(2)}`;

    const elTotal = document.getElementById('card-total');
    if (elTotal) elTotal.innerText = total.toString();

    const elFiltrados = document.getElementById('card-filtrados');
    if (elFiltrados) elFiltrados.innerText = filtrados.toString();
}

document.addEventListener('DOMContentLoaded', carregarDashboard);