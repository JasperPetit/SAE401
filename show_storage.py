import shutil
import os

def format_bytes(bytes_value):
    """Formate une valeur en octets en une chaîne lisible (Go, Mo, Ko)."""
    if bytes_value is None:
        return "N/A"
    for unit in ['B', 'KB', 'MB', 'GB', 'TB']:
        if bytes_value < 1024.0:
            return f"{bytes_value:.2f} {unit}"
        bytes_value /= 1024.0

def show_disk_usage(path='/'):
    """Affiche l'utilisation du disque pour le chemin spécifié."""
    try:
        total, used, free = shutil.disk_usage(path)

        print(f"Informations de stockage pour : {path}")
        print(f"  Espace total: {format_bytes(total)}")
        print(f"  Espace utilisé: {format_bytes(used)}")
        print(f"  Espace libre: {format_bytes(free)}")
        print(f"  Pourcentage utilisé: {used / total:.2%}")

    except FileNotFoundError:
        print(f"Erreur : Le chemin '{path}' n'existe pas.")
    except Exception as e:
        print(f"Une erreur est survenue : {e}")

if __name__ == "__main__":
    # Utilisez le répertoire racine pour obtenir l'information générale du disque
    # Sur Windows, vous pourriez vouloir spécifier une lettre de lecteur comme 'C:/'
    if os.name == 'nt':  # Si le système d'exploitation est Windows
        show_disk_usage('C:/')
    else:  # Pour Linux/macOS
        show_disk_usage('/')
