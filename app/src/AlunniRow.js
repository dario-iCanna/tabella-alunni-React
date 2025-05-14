import { useState } from "react";

export default function AlunniRow(props) {

    const elimina = props.elimina;
    const id = props.id;
    const nome = props.nome;
    const cognome = props.cognome;
    const [conferma, setConferma] = useState(false);






    return (<tr>
        <td>{id}</td>
        <td>{nome}</td>
        <td>{cognome}</td>
        <td>
            {!conferma ? (<button onClick={() => setConferma(true)}>Delete</button>) : (
                <div>
                    Sei sicuro?
                    <button onClick={() => {elimina(id); setConferma(false)}}>SI</button>
                    <button onClick={() => setConferma(false)}>NO</button>
                </div>
            )
            }
            
        </td>
    </tr>);
}