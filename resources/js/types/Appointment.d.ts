export interface AppointmentType {
    id: string,
    situation: string,
    created_at: Date,
    updated_at: Date,
    doctor?: object,
    animal?: object,
    owners?: Array
}

export interface PaginatedData<T> {
    data: T[],
    current_page:  number,
    last_page: number,
    total: number
}
