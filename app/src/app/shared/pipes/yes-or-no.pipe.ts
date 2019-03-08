import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
    name: 'yesOrNo'
})

export class YesOrNoPipe implements PipeTransform {

    constructor() { }

    transform(text: string) {

        if (text === 'S' || text === '1') {
            return 'Sim';
        } else
            if (text === 'N' || text === '0') {
                return 'Não';
            }
        return text;
    }
}
