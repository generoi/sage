import { library as iconLibrary, dom } from '@fortawesome/fontawesome-svg-core'
import {
  faChevronLeft,
  faChevronRight,
} from '@fortawesome/free-solid-svg-icons';

export function init() {
  iconLibrary.add(
    faChevronLeft,
    faChevronRight,
  );
  dom.watch();
}
