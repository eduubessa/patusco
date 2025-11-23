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
