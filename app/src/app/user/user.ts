import { Screen } from './screen';

export interface User {
    id: number;
    name: string;
    cpf: string;
    phone: string;
    email: string;
    profileId: number;
    unit: number;
    class: number;
    image: string;
    screens: Screen[];
}
