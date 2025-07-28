export interface Seat {
    id: number
    section?: string
    row: string
    column: number
    payment_id: number | null
}
