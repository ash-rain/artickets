export interface SeatSection {
    name: string
    price: number
}

export interface Seat {
    id: number
    section?: SeatSection
    row: string
    column: number
    payment_id: number | null
    price: number
}
