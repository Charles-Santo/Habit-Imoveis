<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PerfilController extends Controller
{
    public function edit()
    {
        $user = Auth::user();

        return view('perfil', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $ehAnunciante = Gate::allows('anunciar');

        $regras = [
            'name' => 'required|string|min:3|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];

        $mensagens = [
            'name.required' => 'O nome é obrigatório.',
            'name.min' => 'O nome deve ter no mínimo 3 caracteres.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Digite um e-mail válido.',
            'email.unique' => 'Este e-mail já está em uso por outro usuário.',
            'foto.image' => 'O arquivo da foto precisa ser uma imagem.',
            'foto.mimes' => 'A foto deve ser JPG, PNG ou WEBP.',
            'foto.max' => 'A foto não pode ter mais de 2 MB.',
            'foto.uploaded' => 'Não foi possível enviar a foto. Verifique o tamanho do arquivo.',
            'nome_corretora.required' => 'O nome da corretora é obrigatório para anunciantes.',
            'cnpj_corretora.required' => 'O CNPJ da corretora é obrigatório para anunciantes.',
        ];

        if ($ehAnunciante) {
            $regras['nome_corretora'] = 'required|string|max:255';
            $regras['cnpj_corretora'] = 'required|string|max:18';
        }

        $dados = $request->validate($regras, $mensagens);

        if ($ehAnunciante && !$user->foto && !$request->hasFile('foto')) {
            return redirect()->back()
                ->withErrors(['foto' => 'A foto é obrigatória para anunciantes.'])
                ->withInput();
        }

        $user->name = $dados['name'];
        $user->email = $dados['email'];

        if ($ehAnunciante) {
            $user->nome_corretora = $dados['nome_corretora'];
            $user->cnpj_corretora = $dados['cnpj_corretora'];
        }

        if ($request->hasFile('foto')) {
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }
            $user->foto = $request->file('foto')->store('fotos_perfil', 'public');
        } elseif (!$ehAnunciante && $request->boolean('remover_foto') && $user->foto) {
            Storage::disk('public')->delete($user->foto);
            $user->foto = null;
        }

        $user->save();

        return redirect()->route('perfil.edit')->with('sucesso', 'Perfil atualizado com sucesso!');
    }
}
