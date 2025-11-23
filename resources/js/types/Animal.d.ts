export interface Animal {
    id: string,
    name: string,
    birthday: Date,
    species: string,
    breed: string,
    doctor?: object
}

export interface PaginatedData<T> {
    data: T[],
    current_page:  number,
    last_page: number,
    total: number
}
