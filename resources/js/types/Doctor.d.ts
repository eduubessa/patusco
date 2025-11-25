export interface Doctor {
    id: string
    name: string
    email: string
    role: string
    created_at: string
    updated_at: string
}

export interface PaginatedData<T> {
    data: T[]
    current_page: number
    last_page: number
    total: number
}
