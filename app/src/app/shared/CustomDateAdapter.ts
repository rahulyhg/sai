import { NativeDateAdapter } from '@angular/material';
import * as moment from 'moment';

export class CustomDateAdapter extends NativeDateAdapter {
    format(date: Date, displayFormat: Object): string {
        moment.locale('pt-BR'); // Choose the locale
        const formatString = (displayFormat === 'input') ? 'DD/MM/YYYY' : 'DD/MM/YYYY';
        return moment(date).format(formatString);
    }
}
