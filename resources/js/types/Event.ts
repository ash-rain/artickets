import { Venue } from './Venue'

export interface Event {
    id: number
    title?: string
    date?: string
    description?: string
    image?: string
    venue: Venue
}
