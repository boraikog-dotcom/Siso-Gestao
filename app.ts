export {};

export interface Consulta {
    id_consulta: number;
    paciente_nome: string;
    dentista_nome: string;
    nome_procedimento: string;
    data_consulta: string;
    hora_consulta: string;
    valor_base: number | string | null;
    valor_final?: number | string | null;
}

function parseValorMonetario(valor: string | number | null | undefined): number {
    if (valor === null || valor === undefined || valor === '') return 0;
    const numero: number = parseFloat(valor.toString().replace(',', '.'));
    return isNaN(numero) ? 0 : numero;
}

function atualizarInterface(faturamento: number, total: number, filtrados: number): void {
    const elFaturamento: HTMLElement | null = document.getElementById('card-faturamento');
    if (elFaturamento) elFaturamento.innerText = `R$ ${faturamento.toFixed(2).replace('.', ',')}`;

    const elTotal: HTMLElement | null = document.getElementById('card-total');
    if (elTotal) elTotal.innerText = total.toString();

    const elFiltrados: HTMLElement | null = document.getElementById('card-filtrados');
    if (elFiltrados) elFiltrados.innerText = filtrados.toString();
}

function obterDentistaDestaque(dados: Consulta[]): string {
    if (dados.length === 0) return 'Nenhum';
    
    const contagem: Record<string, number> = {};
    dados.forEach((c: Consulta): void => {
        const dentista: string = c.dentista_nome || 'Não informado';
        contagem[dentista] = (contagem[dentista] || 0) + 1;
    });

    let maisAtendeu: string = '';
    let maxConsultas: number = 0;
    for (const [dentista, total] of Object.entries(contagem)) {
        if (total > maxConsultas) {
            maxConsultas = total;
            maisAtendeu = dentista;
        }
    }
    return `${maisAtendeu} (${maxConsultas} consultas)`;
}

async function carregarDashboard(): Promise<void> {
    try {
        const resposta: Response = await fetch('api.php');
        if (!resposta.ok) throw new Error(`Erro na requisição: ${resposta.status}`);

        const dados: Consulta[] = await resposta.json();
        if (!Array.isArray(dados) || dados.length === 0) {
            atualizarInterface(0, 0, 0);
            return;
        }

        const valoresFormatados: string[] = dados.map((item: Consulta): string => {
            const valor: number = parseValorMonetario(item.valor_final ?? item.valor_base);
            return `R$ ${valor.toFixed(2).replace('.', ',')}`;
        });

        const maisCaros: Consulta[] = dados.filter((item: Consulta): boolean => {
            return parseValorMonetario(item.valor_final ?? item.valor_base) >= 150;
        });

        const faturamento: number = dados.reduce((total: number, item: Consulta): number => {
            return total + parseValorMonetario(item.valor_final ?? item.valor_base);
        }, 0);

        const dentistaPop: string = obterDentistaDestaque(dados);
        console.log('Dentista Destaque:', dentistaPop);
        console.log('Valores formatados via .map():', valoresFormatados);

        atualizarInterface(faturamento, dados.length, maisCaros.length);

    } catch (erro: unknown) {
        console.error('Falha no Dashboard:', erro);
    }
}

document.addEventListener('DOMContentLoaded', carregarDashboard);