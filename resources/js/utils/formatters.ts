export function formatDateTime(dateString: string | undefined): string {
    if(!dateString){
        return '-'
    }

    return new Date(dateString).toLocaleDateString('pt-PT', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        timeZone: 'Europe/Lisbon'
    });
}

export function getInitials(fullName: string | undefined): string {
    if(!fullName || fullName.trim() === ''){
        return '??'
    }

    const nameParts = fullName.trim().split(/\s+/);

    if(nameParts.length === 1){
        return nameParts[0][0].toUpperCase();
    }

    const firstInitial = nameParts[0][0].toUpperCase();
    const lastInitial = nameParts[nameParts.length-1][0].toUpperCase();

    return `${firstInitial}${lastInitial}`;
}

export function generateHashedColor(initials: string | undefined): string {
    if(!initials || initials.length === ''){
        return '#CCCCCC';
    }

    const letters = '0123456789ABCDEF';
    let color = '#';

    for (let i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }

    return color;
}

export function calculateAge(birthday: string | Date | undefined) {
    const currentDate = new Date();
    const birthDate = new Date(birthday);
    const difference = currentDate - birthDate;
    return Math.floor(difference / 31557600000)
}

/**
 * FriendlyIdFormatter.ts
 *
 * Classe utilitária para formatar números sequenciais (registrationNumber)
 * em identificadores amigáveis com padding e prefixo (ex: "V-00782").
 */
export class FriendlyIdFormatter {
    private static readonly DEFAULT_PREFIX: string = 'V';
    private static readonly DEFAULT_LENGTH: number = 5;
    private static readonly SEPARATOR: string = '-';

    /**
     * Formata um número sequencial em um ID amigável com padding e prefixo.
     * * @param registrationNumber O número sequencial a ser formatado (ex: 782).
     * @param prefix O prefixo a usar (default: 'V').
     * @param targetLength O comprimento total desejado para a parte numérica (default: 5).
     * @returns O ID formatado (e.g., "V-00782").
     * @throws {TypeError} Se o número de registo for inválido.
     */
    public static format(
        registrationNumber: number,
        prefix: string = FriendlyIdFormatter.DEFAULT_PREFIX,
        targetLength: number = FriendlyIdFormatter.DEFAULT_LENGTH
    ): string {

        // 1. Validação de Input
        if (!Number.isInteger(registrationNumber) || registrationNumber < 0) {
            throw new TypeError("O registrationNumber deve ser um número inteiro não-negativo.");
        }
        if (targetLength <= 0) {
            throw new Error("O targetLength deve ser um número positivo.");
        }

        // 2. Padding (Adicionar zeros à esquerda)
        const numberString: string = registrationNumber.toString();
        const paddedNumber: string = numberString.padStart(targetLength, '0');

        // 3. Combinação e Formato Final
        // Usa toUpperCase() para consistência visual em hexadecimais, se aplicável
        return `${prefix}${FriendlyIdFormatter.SEPARATOR}${paddedNumber.toUpperCase()}`;
    }
}
