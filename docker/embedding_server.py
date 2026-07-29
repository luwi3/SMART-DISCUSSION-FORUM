from flask import Flask, request, jsonify
from sentence_transformers import SentenceTransformer

app = Flask(__name__)
model = SentenceTransformer('all-MiniLM-L6-v2')


@app.route('/embed', methods=['POST'])
def embed():
    data = request.get_json(silent=True) or {}
    text = data.get('text', '')
    embedding = model.encode(text).tolist()
    return jsonify({'embedding': embedding})


if __name__ == '__main__':
    app.run(host='127.0.0.1', port=5003)
