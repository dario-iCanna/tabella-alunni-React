import React, { useState, useEffect } from 'react';
import AlunniRow from './AlunniRow';


export default function Tabella(props){

    const [data, setData] = useState([]);
    const [formState, setFormState] = useState(-1);
    const [loading, setLoading] = useState(false);
    const [nome, setNome] = useState("");
    const [cognome, setCognome] = useState("");

    const elimina = async (id) =>{
        const response = await fetch(`http://localhost:8080/alunni/${id}` , {method:"DELETE"});
        const body = await response.json();
        await Richiesta();
    }


    const lista = data.map(dato => 
            <AlunniRow elimina={elimina} id={dato.id} nome={dato.nome} cognome={dato.cognome} setFormState={setFormState}/>
    );

   
  
    const Richiesta = async () => {
        setLoading(true);
        const response = await fetch('http://localhost:8080/alunni', {method:"GET"});
        const body = await response.json();
        await setData(body);
    }

    const Ripristina = () => {
        setLoading(false);
        setData([]);
    }

    const gestisciSubmit = async (event) => {

        const response = await fetch('http://localhost:8080/alunni', {headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          }, method:"POST", body: JSON.stringify({nome: nome, cognome: cognome})});
        const body = await response.json();
        await setNome("");
        await setCognome("");
        await Richiesta();
    }


    const modificaAlunno = async (event) => {
        const response = await fetch(`http://localhost:8080/alunni/${formState}`, {headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          }, method:"PUT", body: JSON.stringify({nome: nome, cognome: cognome})});
        const body = await response.json();
        await setNome("");
        await setCognome("");
        await Richiesta();
        setFormState(-1);
    }

    return (
        <>
            
            {data.length > 0 ? (<><table border={1}>
                {lista}
            </table>
                {formState == 0 ? (
                    <>
                        <div>
                            <label>Inserisci nome: 
                                <input 
                                    type="text" 
                                    value={nome}
                                    onChange={(i) => setNome(i.target.value)}
                                />
                            </label>
                            <br />
                            <label>Inserisci cognome: 
                                <input 
                                    type="text" 
                                    value={cognome}
                                    onChange={(i) => setCognome(i.target.value)}
                                />
                            </label>
                            <br />
                        <button onClick={gestisciSubmit}>Salva</button>
                        <button onClick={() => setFormState(-1)}>Annulla</button>
                        </div>
                    </>
                )
                : 
                (
                <>
                    {formState > 0 ? (
                        <>
                            <p>Modifica Alunno {formState} </p>
                            <div>
                            <label>Inserisci nome: 
                                <input 
                                    type="text" 
                                    value={nome}
                                    onChange={(i) => setNome(i.target.value)}
                                />
                            </label>
                            <br />
                            <label>Inserisci cognome: 
                                <input 
                                    type="text" 
                                    value={cognome}
                                    onChange={(i) => setCognome(i.target.value)}
                                />
                            </label>
                            <br />
                        <button onClick={modificaAlunno}>Modifica</button>
                        <button onClick={() => setFormState(-1)}>Annulla</button>
                        </div>
                        </>

                    ):(
                        <>
                        <button onClick={() => setFormState(0)}>aggiungi alunno</button>
                    </>)}
                </>
                )}
                <br />
                <button onClick={Ripristina}>Torna Indietro</button>
            </>
            ): (
            loading ?  (<p>Caricando...</p>): (<button onClick={Richiesta}>Stampa Alunni</button>)
            )}
            
        </>
    );
    
}