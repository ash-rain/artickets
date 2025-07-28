import { Seat } from './Seat'

export interface Section {
    id: number
    name: string
    columns: number
    seats: Seat[]
}
